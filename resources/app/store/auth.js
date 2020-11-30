export const state = () => ({
    user: null
})

export const getters = {
    user: (state) => {
        return state.user
    },
}

export const mutations = {
    STORE(state, payload) {
        state.user = payload
    },
}

export const actions = {
    store({ commit }, payload) {
        commit('STORE', payload)
    }
}
