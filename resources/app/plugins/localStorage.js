import createPersistedState from 'vuex-persistedstate'

export default ({ store }) => {
    const subdomain =  window.location.host.split('.')[1] ? window.location.host.split('.')[0] : Date.now()
    createPersistedState({
        key: `productbox-${subdomain}`,
        fetchBeforeUse: true,
        paths: ['auth.token'],
    })(store)
}
