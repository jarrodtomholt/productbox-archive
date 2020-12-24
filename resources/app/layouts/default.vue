<template>
    <div class="h-screen w-screen overflow-hidden bg-gray-50 font-sans">
        <Loader v-if="$fetchState.pending" />
        <div v-else class="flex flex-col flex-1">
            <Header />
            <h1>{{ settings.messageOfTheDay }}</h1>
            <Nuxt />
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Loader from '~/components/loader'
import Header from '~/components/header'

export default {
    name: 'defaultLayout',
    components: {
        Loader,
        Header,
    },
    computed: {
        ...mapGetters({
            settings: 'settings/all',
        }),
    },
    data() {
        return {
        }
    },
    async fetch () {
        await this.$axios.get('/').then(response => {
            this.$store.dispatch('categories/store', response.categories)
            this.$store.dispatch('settings/store', response.settings)
        })
    },
    fetchOnServer: false,
    fetchDelay: 500,
}
</script>
