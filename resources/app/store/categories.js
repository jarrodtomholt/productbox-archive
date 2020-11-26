export const state = () => ({
    all: null,
})

export const getters = {
    all: (state) => {
        return state.all
    }
}

export const mutations = {
    STORE(state, payload) {
        state.all = payload
    },
}

export const actions = {
    store({ commit }, payload) {
        commit('STORE', payload)
    }
}
