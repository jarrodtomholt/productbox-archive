import createPersistedState from 'vuex-persistedstate'

export default ({ store }) => {
    createPersistedState({
        key: 'productbox',
        reducer (store) {
            if (store.auth.user) {
                return store.auth.user.token
            }
            return {}
        }
    })(store)
}
