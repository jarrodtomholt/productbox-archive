<template>
    <div class="flex flex-col flex-1">
        <p>{{ settings.messageOfTheDay }}</p>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
    computed: {
        ...mapGetters({
            settings: 'settings/all',
        }),
    }
}
</script>
