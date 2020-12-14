<template>
    <div v-click-away="close" class="z-10 sm:relative">

        <button @click="open = !open" class="h-14 w-14 inline-flex items-center justify-center border border-transparent leading-4 font-medium rounded-full shadow-sm transition ease-out duration-300" :class="buttonClasses">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
        </button>

        <transition enter-active-class="transition ease-out duration-100" enter-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
            <div v-if="open" class="origin-top-right absolute right-0 mt-2 w-screen sm:max-w-md sm:px-0">
                <div v-if="content" class="bg-white sm:rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden">
                    <div class="max-h-96 z-20 relative grid gap-6 px-5 py-6 sm:gap-8 sm:p-8 overflow-y-auto">
                        <div v-for="(item, rowId) in content" :key="rowId" class="grid grid-col-2">
                            <div class="inline-flex w-full items-end justify-between truncate">
                                <h4 class="text-gray-800 font-medium">
                                    {{ item.name }}
                                </h4>
                                <small class="text-xl text-gray-900 font-medium">
                                    ${{ item.subtotal|formatMoney }}
                                </small>
                            </div>
                            <div class="inline-flex w-full items-start justify-between">
                                <div v-if="item.options" class="space-y-1">
                                    <p v-if="item.options.variant" class="text-gray-500 text-sm font-regular">
                                        {{ item.options.variant.name }}
                                    </p>
                                    <div>
                                        <p v-if="item.options.options" v-for="(option, index) in item.options.options" class="text-gray-400 text-sm font-light">
                                            {{ option.name }}
                                        </p>
                                    </div>
                                </div>
                                <div class="inline-flex items-center mt-1">
                                    <span class="relative z-0 inline-flex shadow-sm rounded-md">
                                        <button @click="decrement(item)" type="button" class="relative inline-flex items-center px-3 py-1 rounded-l-md border border-gray-300 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                            -
                                        </button>
                                        <span class="-ml-px relative inline-flex items-center px-3 py-1 border border-gray-300 bg-white text-xs font-medium text-gray-700">
                                            {{ item.qty }}
                                        </span>
                                        <button @click="increment(item)" type="button" class="-ml-px relative inline-flex items-center px-3 py-1 rounded-r-md border border-gray-300 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                            +
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="relative group w-full items-center justify-center text-center px-5 py-5 space-y-6 border border-transparent leading-4 font-medium shadow-sm text-indigo-50 bg-indigo-600 transition duration-300 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-300">
                        <span class="absolute left-0 inset-y-0 flex items-center px-5 sm:px-8 ">
                            <svg class="h-10 w-10 text-indigo-500 transition duration-300 group-hover:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        </span>
                        ${{ total|formatMoney }}
                    </button>
                </div>
                <div v-else class="bg-white sm:rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden">
                    <div class="text-center relative px-5 py-6 space-y-2">
                        <h6 class="text-center text-lg font-extrabold text-gray-900">
                            Not hungry?
                        </h6>
                        <p class="text-gray-400 text-sm font-light">
                            Try adding something delicious from the menu
                        </p>
                    </div>
                </div>
            </div>
        </transition>

    </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
    name: 'Cart',
    computed: {
        ...mapGetters({
            content: 'cart/content',
            coupon: 'cart/coupon',
            subtotal: 'cart/subtotal',
            total: 'cart/total',
        }),
        buttonClasses() {
            if (this.content) {
                return 'text-indigo-50 bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'
            }
            return 'text-gray-400 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300'
        }
    },
    data() {
        return {
            open: false,
        }
    },
    methods: {
        close() {
            this.open = false
        },
        increment(item) {
            if (item.qty === 0) {
                return
            }
            let clone = Object.assign({}, item)
            this.$axios.put('cart', { rowId: clone.rowId, quantity: ++clone.qty }).then(response => {
                this.$store.dispatch('cart/store', response)
            })
        },
        decrement(item) {
            if (item.qty === 0) {
                return
            }
            let clone = Object.assign({}, item)
            this.$axios.put('cart', { rowId: clone.rowId, quantity: --clone.qty }).then(response => {
                this.$store.dispatch('cart/store', response)
            })
        },
    },
    async fetch() {
        await this.$axios.get('cart').then(response => {
            this.$store.dispatch('cart/store', response)
        })
    },
    fetchOnServer: false,
    fetchDelay: 500,
}
</script>
