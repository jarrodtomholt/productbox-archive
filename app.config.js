import { resolve } from 'path'

// eslint-disable-next-line import/no-absolute-path
import original from './resources/app/nuxt.config'

const config = { ...original }

config.rootDir = resolve('./resources/app')
config.modules = [...(config.modules || []), 'nuxt-laravel']
config.laravel = {
  dotEnvExport: true
}

config.router = {
  ...config.router,
  base: '/app/'
}

export default config
