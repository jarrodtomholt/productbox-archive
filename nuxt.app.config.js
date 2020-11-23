import { resolve } from 'path'

// eslint-disable-next-line import/no-absolute-path
import original from './resources/app/nuxt.config'

const config = { ...original }

config.rootDir = resolve('./resources/app')
config.generate = {
    dir: resolve('./public/app')
}

// config.modules = [...(config.modules || []), 'nuxt-laravel']
// config.laravel = {
//     server: false,
//     dotEnvExport: false,
// }

config.router = {
    ...config.router,
    base: process.env.NODE_ENV === 'production' ? '/app/' : '/dev/'
}

export default config
