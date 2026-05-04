export * from './versionrc.base.js'

export const bumpFiles = [
  {
    filename: 'package.json',
    type: 'json',
  },
  {
    filename: 'style.css',
    updater: {
      /**
       * @param {String} contents
       */
      readVersion: (contents) => {
        const match = contents.match(/Version:\s*([\d.]+)/)

        return match[1]
      },

      /**
       * @param {String} contents
       * @param {String} version
       */
      writeVersion: (contents, version) => {
        return contents.replace(/Version:\s*[\d.]+/, `Version: ${version}`)
      },
    },
  },
]
