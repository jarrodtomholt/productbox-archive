<template>
    <div class="flex flex-col flex-1 px-8 py-8 my-4 overflow-hidden">

        <div class="flex flex-row flex-1 space-x-6">
            <category-list />

            <div>
                <h1>{{ item.name }}</h1>
                <p>{{ item.description }}</p>

                <div v-if="item.variants">
                    <fieldset class="mt-6">
                        <legend id="variants-label">
                            Select an option
                        </legend>
                        <ul class="space-y-4" role="radiogroup" aria-labelledby="variants-label">
                            <li v-for="(variant, index) in item.variants" :key="`variant#${index}`" :id="`variant#${index}`" :tabindex="index" @click="selectVariant(variant)" role="radio" aria-checked="true" class="group relative bg-white rounded-lg shadow-sm cursor-pointer transition duration-300 focus:outline-none focus:ring-1 focus:ring-offset-2 focus:ring-indigo-300">
                                <div class="rounded-lg border border-gray-300 bg-white px-6 py-4 hover:border-gray-400 sm:flex sm:justify-between">
                                    <div class="flex items-center">
                                        <div class="text-sm">
                                            <p class="font-medium text-gray-900">
                                                {{ variant.name }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-2 flex text-sm sm:mt-0 sm:block sm:ml-4 sm:text-right">
                                        <div class="font-medium text-gray-900">
                                            ${{ variant.price }}
                                        </div>
                                    </div>
                                </div>
                                <div class="absolute inset-0 rounded-lg border-2 pointer-events-none" :class="selectedVariantClasses(variant)" aria-hidden="true"></div>
                            </li>
                        </ul>
                    </fieldset>
                </div>
                <div v-else>
                    <p>${{ item.price|formatMoney }}</p>
                </div>

                <hr class="border-t my-6">

                <fieldset v-if="item.options">
                    <legend id="options-label">
                        Add Additional Options
                    </legend>
                    <ul class="space-y-4" role="radiogroup" aria-labelledby="options-label">
                        <li v-for="(option, index) in item.options" :key="`option#${index}`" :id="`option#${index}`" :tabindex="index" @click="selectOption(option)" role="radio" aria-checked="true" class="group relative bg-white rounded-lg shadow-sm cursor-pointer transition duration-300 focus:outline-none focus:ring-1 focus:ring-offset-2 focus:ring-indigo-300">
                            <div class="rounded-lg border border-gray-300 bg-white px-6 py-4 hover:border-gray-400 sm:flex sm:justify-between">
                                <div class="flex items-center">
                                    <div class="text-sm">
                                        <p class="font-medium text-gray-900">
                                            {{ option.name }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-2 flex text-sm sm:mt-0 sm:block sm:ml-4 sm:text-right">
                                    <div class="font-medium text-gray-900">
                                        ${{ option.price }}
                                    </div>
                                </div>
                            </div>
                            <div class="absolute inset-0 rounded-lg border-2 pointer-events-none" :class="selectedOptionClasses(option)" aria-hidden="true"></div>
                        </li>
                    </ul>
                </fieldset>

                <button @click="addToCart" class="mt-12 relative group w-full items-center justify-center text-center px-5 py-5 space-y-6 border border-transparent leading-4 font-medium shadow-sm text-indigo-50 bg-indigo-600 rounded-lg transition duration-300 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-300">
                    <span class="absolute left-0 inset-y-0 flex items-center px-5 sm:px-8 ">
                        <svg class="h-10 w-10 text-indigo-500 transition duration-300 group-hover:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </span>
                    Add to Cart
                </button>
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
            let selections = {}

            if (this.variant) {
                selections = { ...selections, variant: this.variant }
            }

            if (this.options.length) {
                selections = { ...selections,  options: this.options }
            }

            this.$axios.post('cart', { item: this.item.slug, selections: selections }).then(response => {
                this.$store.dispatch('cart/store', response)
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
        },
        selectedVariantClasses(variant) {
            if (this.variant && this.variant.slug === variant.slug) {
                return 'border-indigo-500'
            }

            return 'border-transparent'
        },
        selectedOptionClasses(variant) {
            let index = this.options.findIndex(option => {
                return option.slug === option.slug
            })

            if (index >= 0) {
                return 'border-indigo-500'
            }

            return 'border-transparent'
        },
    },
    mounted() {
        if (this.item.variants) {
            this.selectVariant(this.item.variants[0])
        }
    }
}
</script>
