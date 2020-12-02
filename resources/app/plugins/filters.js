import Vue from 'vue'
import moment from 'moment'

Vue.filter('formatMoney', value => {
    if (value === null || value === '' || !parseFloat(value)) {
        return value
    }

    return `${parseFloat(value).toLocaleString('en-AU', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`
})

Vue.filter('diffForHumans', value => {
    return moment(value).fromNow()
})
