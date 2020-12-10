<template>
    <div class="flex flex-col flex-1 px-8 py-8 my-4 overflow-hidden">

        <div class="flex flex-row flex-1 space-x-6">
            <category-list />

            <div>
                <h1>{{ item.name }}</h1>

                <div v-if="variant">
                    <p>${{ variant.price }}</p>
                </div>
                <div v-else>
                    <p>${{ item.price|formatMoney }}</p>
                </div>

                <hr>
                <div>
                    <ul v-if="item.variants">
                        <li>Variants</li>
                        <li @click="selectVariant(variant)" v-for="(variant, index) in item.variants">
                            {{ variant.name }}
                            ${{ variant.price }}
                        </li>
                    </ul>
                    <ul v-if="item.options">
                        <li>Options</li>
                        <li @click="selectOption(option)" v-for="(option, index) in item.options">
                            {{ option.name }}
                            ${{ option.price }}
                        </li>
                    </ul>
                </div>

                <button @click="addToCart">Add to Cart</button>
            </div>

        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import categoryList from '~/components/categoryList'

export default {
    name: 'Item',
    components: {
        categoryList,
    },
    computed: {
        ...mapGetters({
            categories: 'categories/all',
        }),
        item() {
            return this.categories.find(category => {
                return category.slug === this.$route.params.category
            }).items.find(item => {
                return item.slug === this.$route.params.item
            })
        },
    },
    data() {
        return {
            variant: null,
            options: [],
        }
    },
    methods: {
        addToCart() {
            let data = {
                item: this.item.slug,
            }

            if (this.variant) {
                data = { ...data, ...{ selections: { variant: this.variant.slug } } }
            }

            if (this.options.length) {

            }

            this.$axios.post('cart', data).then(response => {
                this.$store.dispatch('cart/store', response)
            }).finally(() => {
                this.variant = null
                this.options = []
            })
        },
        selectVariant(variant) {
            this.variant = variant
        },
        selectOption(option) {
            let index = this.options.findIndex(option => {
                return option.slug === option.slug
            })

            if (index >= 0) {
                this.options.splice(index, 1)
                return
            }

            this.options.push(option)
        }
    }
}
</script>
