import Vue from 'vue'

export default function ({ app, store, $axios, redirect }, inject) {
    const axios = $axios.create({
        timeout: 30000,
        withCredentials: true,
        headers: {
            common: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        },
    })

    axios.setBaseURL(`${window.location.origin}/api/`)

    axios.onRequest(config => {
        // let authToken = store.getters['auth/token']
        // if (authToken) {
        //     config.headers.common = { 'Authorization': `Bearer ${authToken}` }
        // }
    })

    axios.onResponse(response => {
        return Promise.resolve(response.hasOwnProperty('data') ? response.data : response)
    })

    axios.onError(error => {
        const code = parseInt(error.response && error.response.status)
        console.warn(`AXIOS ERROR: status code ${code} received`)
        return Promise.reject(error)
    })

    inject('axios', axios)
}
