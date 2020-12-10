<template>
    <div class="flex flex-col flex-1 px-8 py-8 my-4 overflow-hidden">

        <div class="flex flex-row flex-1 space-x-6">
            <category-list />

            <category-items />

        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import categoryList from '~/components/categoryList'
import categoryItems from '~/components/categoryItems'

export default {
    name: 'CategoryIndex',
    components: {
        categoryList,
        categoryItems,
    },
    computed: {
        ...mapGetters({
            categories: 'categories/all',
        }),
        category() {
            return this.categories.find(category => {
                return category.slug === this.$route.params.category
            })
        }
    },
}
</script>
