import { basename } from 'node:path'
import { existsSync, readFileSync } from 'node:fs'

const cwd = process.cwd()
const pkgName = basename(cwd)

/**
 * @typedef {Object} PatternPairs
 * @property {RegExp} pattern
 * @property {(version: string) => string} replace
 */

/**
 * @param {PatternPairs[]} patterns
 * @returns
 */
const createUpdater = (patterns) => ({
  /**
   * @param {string} content
   */
  readVersion: (content) => {
    let result = ''

    for (const { pattern } of patterns) {
      const match = content.match(pattern)

      if (match?.groups) {
        result = match.groups.version
        break
      }
    }

    return result
  },

  /**
   * @param {string} content
   * @param {string} version
   */
  writeVersion: (content, version) =>
    patterns.reduce(
      (content, { pattern, replace }) =>
        content.replace(pattern, replace(version)),
      content,
    ),
})

/**
 * @typedef {Object} BumpFile
 * @property {string} filename
 * @property {Object} updater
 *
 * @type {BumpFile[]}
 */
const files = []
const basePattern = {
  pattern: /Version:\s*(?<version>[\d.]+(-\w.*)?)/,
  replace: (version) => `Version: ${version}`,
}

if (existsSync(`${cwd}/style.css`)) {
  files.push({
    filename: 'style.css',
    updater: createUpdater([basePattern]),
  })
}

if (existsSync(`${cwd}/composer.json`)) {
  const composerJson = JSON.parse(readFileSync(`${cwd}/composer.json`, 'utf8'))

  if ('version-constants' in (composerJson?.extra || {})) {
    for (const [key, filename] of Object.entries(
      composerJson.extra['version-constants'] || {},
    )) {
      if (!existsSync(`${cwd}/${filename}`)) continue

      files.push({
        filename,
        updater: createUpdater([
          basePattern,
          {
            pattern: new RegExp(`'${key}',\\s*'(?<version>[\\d.]+(-\\w.*)?)'`),
            replace: (version) => `'${key}', '${version}'`,
          },
        ]),
      })
    }
  }
}

export const bumpFiles = [
  {
    filename: 'package.json',
    type: 'json',
  },
  ...files,
]

export const releaseCommitMessageFormat = `bump(${pkgName}): release v{{currentTag}}`

export const writerOpts = {
  transform: (commit, { packageData } = {}) => {
    const repo = packageData?.repository || {}

    if (!repo.directory || commit?.scope !== basename(repo.directory)) {
      return null
    }

    return commit
  },
}
