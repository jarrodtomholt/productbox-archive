export const state = () => ({
    content: null,
    coupon: null,
    subtotal: null,
    total: null,
})

export const getters = {
    content: (state) => {
        return state.content
    },
    coupon: (state) => {
        return state.coupon
    },
    subtotal: (state) => {
        return state.subtotal
    },
    total: (state) => {
        return state.total
    },
}

export const mutations = {
    STORE(state, payload) {
        state.content = payload.content
        state.coupon = payload.coupon
        state.subtotal = payload.subtotal
        state.total = payload.total
    },
}

export const actions = {
    store({ commit }, payload) {
        commit('STORE', payload)
    }
}
