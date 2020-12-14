<template>
    <div class="w-1/6">
        <div class="space-y-1 mt-6" role="group" aria-labelledby="projects-headline">
          <NuxtLink :to="{ name: 'category', params: { category: category.slug }}" v-for="(category, index) in categories" :key="`category#${category.slug}`" class="group flex items-center px-3 py-2 text-sm font-medium text-gray-500 rounded-md hover:text-indigo-600 uppercase tracking-wider" :class="{ 'text-indigo-600' : isSelected(category) }">
            <span class="inline-flex items-center justify-between flex-1 truncate px-2 -mx-2">
                {{ category.name }}
            </span>
          </NuxtLink>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
    name: 'CategoryList',
    computed: {
        ...mapGetters({
            categories: 'categories/all',
        }),
    },
    methods: {
        isSelected(category) {
            return category.slug === this.$route.params.category
        }
    }
}
</script>
