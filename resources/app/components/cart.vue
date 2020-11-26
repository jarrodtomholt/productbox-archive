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
                        <div v-for="(item, rowId) in content">
                            {{ item.name }} qty:{{ item.qty }} ${{ item.subtotal }}
                            <button @click="remove(item)">Remove</button>
                        </div>
                    </div>
                    <button class="w-full items-center justify-center text-center px-5 py-5 bg-gray-50 space-y-6">
                        ${{ total }}
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
        remove(item) {
            this.$axios.delete('/cart', { params: { rowId: item.rowId } }).then(response => {
                this.$store.dispatch('cart/store', response)
            })
        }
    },
    async fetch() {
        await this.$axios.get('/cart').then(response => {
            this.$store.dispatch('cart/store', response)
        })
    }
}
</script>
