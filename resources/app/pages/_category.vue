<template>
    <div>
        <div class="px-4 pt-6 pb-2">
            <h1 class="text-2xl font-bold w-2/3 leading-10">
                {{ settings.messageOfTheDay }}
            </h1>
        </div>

        <nav class="bg-gradient-to-b from-gray-50 via-gray-50 to-transparent flex flex-row flex-1 pb-6 px-4 font-light text-sm sticky -top-4 z-10">
            <ul class="flex flex-row flex-1 flex-wrap-0 overflow-x-auto space-x-12 py-4">
                <li v-for="(category, index) in categories" :key="`nav-category#${index}`" class="py-2 border-transparent border-b-2" :class="{'border-solid border-indigo-600' : index === 0}">
                    {{ category.name }}
                </li>
            </ul>
        </nav>

        <div class="h-full -mt-6 px-4 py-2">
            <ul class="grid grid-cols-2 gap-x-3 gap-y-8 mt-4">
                <li v-for="(item, index) in categories[0].items" :key="`items#${item.slug}`" class="flex flex-col flex-1 items-center justify-end rounded-md border shadow-sm px-3 py-4 bg-white">
                    <div class="flex flex-col flex-1 items-center">
                        <div class="transform -translate-y-1/3 w-24 h-24 rounded-full overflow-hidden">
                            <img src="https://www.fillmurray.com/200/200">
                        </div>
                        <h3 class="-mt-4 text-gray-700 font-medium leading-6 tracking-wide">
                            {{ item.name }}
                        </h3>
                    </div>
                    <h4 class="mt-3 font-medium leading-5 tracking-wide text-indigo-600">
                        ${{ item.price }}
                    </h4>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
    name: 'CategoryHome',
    components: {
    },
    computed: {
        ...mapGetters({
            settings: 'settings/all',
            categories: 'categories/all',
        }),
        category() {
            return this.categories.find(category => {
                return category.slug === this.$route.params.category
            }) || this.categories[0]
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
    },
}
</script>
