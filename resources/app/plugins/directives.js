import Vue from 'vue'

Vue.directive('click-away', {
    bind: (el, binding, vnode) => {
        el.clickOutsideEvent = (event) => {
            if (!(el == event.target || el.contains(event.target))) {
                vnode.context[binding.expression](event)
                event.stopPropagation()
            }
        }
        document.body.addEventListener('click', el.clickOutsideEvent)
    },
    unbind: (el) => {
        document.body.removeEventListener('click', el.clickOutsideEvent)
    },
})
