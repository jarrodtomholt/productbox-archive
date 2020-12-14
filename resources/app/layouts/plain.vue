<template>
    <div class="h-screen w-screen overflow-hidden bg-gray-50 font-sans">
        <Nuxt />
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Loader from '~/components/loader'

export default {
    name: 'plainLayout',
    components: {
        Loader,
    },
    data() {
        return {

        }
    },
}
</script>
