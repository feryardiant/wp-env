import { basename } from 'node:path'
import { existsSync, readFileSync } from 'node:fs'

const cwd = process.cwd()
const pkgName = basename(cwd)

/**
 * @typedef {Object} PatternPairs
 * @property {String} pattern
 * @property {(String) => String} replace
 *
 * @param {PatternPairs[]} patterns
 * @returns
 */
const createUpdater = (patterns) => {
  return {
    /**
     * @param {String} content
     */
    readVersion: (content) => {
      let result = ''

      for (const { pattern } of patterns) {
        const match = content.match(pattern)

        if (match) {
          result = match[1]
          break
        }
      }

      return result
    },

    /**
     * @param {String} content
     * @param {String} version
     */
    writeVersion: (content, version) => {
      return patterns.reduce((content, { pattern, replace }) => {
        return content.replace(pattern, replace(version))
      }, content)
    },
  }
}

/**
 * @typedef {Object} BumpFile
 * @property {String} filename
 * @property {typeof updater} updater
 *
 * @type {BumpFile[]}
 */
const files = []

if (existsSync(`${cwd}/style.css`)) {
  files.push({
    filename: 'style.css',
    updater: createUpdater([
      {
        pattern: /Version:\s*([\d.]+)/,
        replace: (version) => `Version: ${version}`,
      },
    ]),
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
          {
            pattern: /Version:\s*([\d.]+)/,
            replace: (version) => `Version: ${version}`,
          },
          {
            pattern: new RegExp(`'${key}',\\s'([\\d.]+)'`),
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
