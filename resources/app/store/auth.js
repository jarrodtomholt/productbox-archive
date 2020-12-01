export const state = () => ({
    user: null,
    token: null,
})

export const getters = {
    user: (state) => {
        return state.user
    },
    token: (state) => {
        return state.token
    },
}

export const mutations = {
    STORE(state, payload) {
        state.user = payload
        state.token = payload.token ?? null
    },
}

export const actions = {
    store({ commit }, payload) {
        commit('STORE', payload)
    }
}
