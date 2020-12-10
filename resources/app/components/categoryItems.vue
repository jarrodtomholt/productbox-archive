<template>
    <div class="flex flex-col flex-1">
        <div class="text-right mb-4">
            <span class="inline-flex shadow-sm rounded-md">
              <button @click="display = 'rows'" type="button" class="relative inline-flex items-center px-2 py-2 rounded-l-md bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 text-sm font-medium text-gray-400 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition duration-100" :class="{ 'bg-indigo-600 text-indigo-50 hover:bg-indigo-500': display === 'rows' }">
                <span class="sr-only">set display to rows</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                </button>
                <button @click="display = 'cols'" type="button" class="-ml-px relative inline-flex items-center px-2 py-2 rounded-r-md bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 text-sm font-medium text-gray-400 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition duration-100" :class="{ 'bg-indigo-600 text-indigo-50 hover:bg-indigo-500': display === 'cols' }">
                    <span class="sr-only">set display to columns</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>
            </span>
        </div>
        <div class="grid gap-6 flex-1 overflow-y-auto" :class="`grid-${display}-4`">
            <NuxtLink :to="{ name: 'category-item', params: { category: category.slug, item: item.slug }}" v-for="(item, index) in category.items" :key="`item#${item.slug}`" class="p-6 md:px-10 md:py-6 bg-gradient-to-b rounded-xl leading-6 font-semibold from-transparent to-gray-100 shadow-sm">
                <img src="https://www.fillmurray.com/200/200" class="h-48 w-48 rounded-full">
                <h3>{{ item.name }}</h3>
                <p>${{ item.price }}</p>
            </NuxtLink>>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
    name: 'CategoryItems',
    computed: {
        ...mapGetters({
            categories: 'categories/all',
        }),
        category() {
            return this.categories.find(category => {
                return category.slug === this.$route.params.category
            }) || this.categories[0]
        }
    },
    data() {
        return {
            display: 'cols',
        }
    }
}
</script>
