const path = require('path');
var Encore = require('@symfony/webpack-encore')

Encore
    .setOutputPath('public/build')
    .setPublicPath('/build')
    .addEntry('header_auth', './assets/vue/components/HeaderAuth/index.js')
    .addEntry('change_number', './assets/vue/components/ChangeNumber/index.js')
    .addEntry('confirm_number', './assets/vue/components/ConfirmNumber/index.js')
    .addEntry('header_auth_mobile', './assets/vue/components/HeaderAuthMobile/index.js')
    .addEntry('false_request', './assets/vue/components/UI/FalseRequest/index.js')
    .addEntry('user_profile', './assets/vue/components/UserProfile/index.js')
    .addEntry('fill_info', './assets/vue/components/FillInfo/index.js')
    .addEntry('specialist_profile', './assets/vue/components/SpecialistProfile/index.js')
    .addEntry('head_catalog_search', './assets/vue/components/HeaderCatalogSearch/index.js')
    .addEntry('search', './assets/vue/components/Catalog/Search/index.js')
    .addEntry('filter', './assets/vue/components/Catalog/Filter/index.js')
    .addEntry('favourite', './assets/vue/components/Catalog/Favourite/index.js')
    .addEntry('sorting', './assets/vue/components/Catalog/Sorting/index.js')
    .addEntry('navigation', './assets/vue/components/Catalog/Navigation/index.js')
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSassLoader()
    .enableVueLoader(() => {}, { runtimeCompilerBuild: false })
;

let config = Encore.getWebpackConfig();
config.devServer = {
    allowedHosts: "all"
}
config.resolve = {
    alias: {
        '@' : path.resolve(__dirname, './assets/vue/')
    }
}

module.exports = config;
