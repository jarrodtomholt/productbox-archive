<template>
    <div class="px-4 text-right">
        <p class="capitalize text-gray-700 text-lg leading-snug font-bold">
            {{ currentStatus }}
        </p>
        <p class="text-gray-400 text-sm font-light">
            {{ nextOpenTime }} to {{ nextCloseTime }} {{ nextOpenDate }}
        </p>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import moment from 'moment'

export default {
    name: 'Status',
    computed: {
        ...mapGetters({
            settings: 'settings/all',
        }),
        currentStatus() {
            return this.settings.openingHours.isOpen ? 'open' : 'closed'
        },
        nextOpenDate() {
            return moment().calendar(this.settings.openingHours.nextOpen, {
                sameDay: '[Today]',
                lastDay: '[Tomorrow]',
                lastWeek: 'D-M-Y',
            });
        },
        nextOpenTime() {
            if (this.settings.openingHours.isOpen) {
                return 'Now'
            }

            return moment(this.settings.openingHours.nextOpen).format('LT')
        },
        nextCloseTime() {
            return moment(this.settings.openingHours.nextClose).format('LT')
        }
    },
}
</script>
