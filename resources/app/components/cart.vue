<template>
    <div v-click-away="close" class="relative">
        <button @click="open = !open" class="h-14 w-14 inline-flex items-center justify-center border border-transparent leading-4 font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
        </button>
        <transition enter-active-class="transition ease-out duration-100" enter-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
            <div v-if="open" class="origin-top-right absolute right-0 mt-2 w-screen sm:max-w-md sm:px-0">
                <div v-if="content" class="bg-white sm:rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden">
                    <div class="max-h-96 z-20 relative grid gap-6 px-5 py-6 sm:gap-8 sm:p-8 overflow-y-auto">
                        <div v-for="(item, rowId) in content" :key="rowId" class="grid grid-col-2">
                            <div class="inline-flex w-full items-center justify-between">
                                <h4 class="text-gray-800 font-medium">{{ item.name }}</h4>
                                <small class="text-xl text-gray-900 font-medium">${{ item.subtotal }}</small>
                            </div>
                            <div class="inline-flex w-full items-center justify-between">
                                <p class="text-gray-400 text-sm font-light">variants|options</p>
                                <div class="inline-flex items-center">
                                    <span class="relative z-0 inline-flex shadow-sm rounded-md">
                                        <button @click="decrement(item)" type="button" class="relative inline-flex items-center px-3 py-1 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                            -
                                        </button>
                                        <span class="-ml-px relative inline-flex items-center px-3 py-1 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                            {{ item.qty }}
                                        </span>
                                        <button @click="increment(item)" type="button" class="-ml-px relative inline-flex items-center px-3 py-1 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                            +
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="w-full items-center justify-center text-center px-5 py-5 bg-gray-50 space-y-6 border border-transparent leading-4 font-medium shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-300">
                        ${{ total }} Checkout
                    </button>
                </div>
                <div v-else class="bg-white sm:rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden">
                    <div class="text-center relative grid gap-6 px-5 py-6 sm:gap-8 sm:p-8">
                        <p>Your cart is empty</p>
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
            this.$axios.put('cart', { rowId: item.rowId, quantity: ++item.qty }).then(response => {
                this.$store.dispatch('cart/store', response)
            })
        },
        decrement(item) {
            this.$axios.put('cart', { rowId: item.rowId, quantity: --item.qty }).then(response => {
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
