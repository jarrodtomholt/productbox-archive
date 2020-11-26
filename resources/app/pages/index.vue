<template>
    <div class="flex-1 space-x-12 px-8 py-8 mt-8 overflow-hidden">
        <div class="w-1/2">
            <div class="border-l-4 border-gray-600 p-8">
                <h2 class="text-gray-900 text-6xl leading-snug font-semibold">
                    {{ settings.messageOfTheDay }}
                </h2>
                <p class="mt-8 text-gray-400 font-light">Lorem ipsum dolor sit amet consectetur adipisicing elit. Sapiente veniam aspernatur, nihil iure repudiandae alias quidem.</p>
            </div>
            <div class="px-4 mt-12">
                <button type="button" class="inline-flex items-center px-12 py-4 border border-transparent leading-4 font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    View Menu
                </button>
            </div>
        </div>
        <div class="w-1/2 grid grid-cols-2 gap-12 overflow-y-auto">
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
