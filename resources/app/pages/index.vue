<template>
    <div class="flex flex-col flex-1 space-x-12 px-8 py-8 my-4 overflow-hidden">
<div class="text-right mb-4">
<span class="inline-flex shadow-sm rounded-md">
  <button @click="display = 'rows'" type="button" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
    <span class="sr-only">set display to rows</span>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
    </svg>
  </button>
  <button @click="display = 'cols'" type="button" class="-ml-px relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
    <span class="sr-only">set display to columns</span>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
    </svg>
  </button>
</span>
</div>
        <div class="grid gap-6 overflow-y-auto" :class="`grid-${display}-6`">
            <div v-for="(item, index) in categories[0].items" class="p-6 md:px-10 md:py-6 bg-gradient-to-b rounded-xl leading-6 font-semibold from-transparent to-gray-100 shadow-sm">
                <img src="https://www.fillmurray.com/200/200" class="h-48 w-48 rounded-full">
                <h3>{{ item.name }}</h3>
                <p>${{ item.price }}</p>
                <button @click="addToCart(item)">Add To Cart</button>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
    computed: {
        ...mapGetters({
            settings: 'settings/all',
            categories: 'categories/all',
        }),
    },
    data() {
        return {
            display: 'cols',
        }
    },
    methods: {
        addToCart(item) {
            this.$axios.post('/cart', {
                item: item.slug
            }).then(response => {
                this.$store.dispatch('cart/store', response)
            })
        }
    }
}
</script>
