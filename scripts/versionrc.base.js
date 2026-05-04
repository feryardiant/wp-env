import { basename } from 'node:path'

export const writerOpts = {
  transform: (commit, { packageData } = {}) => {
    const repo = packageData?.repository || {}

    if (!repo.directory || commit?.scope !== basename(repo.directory)) {
      return false
    }

    return commit
  },
}
