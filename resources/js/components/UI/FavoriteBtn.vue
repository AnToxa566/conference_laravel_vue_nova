<template>
    <v-icon
        size="large"
        @click="this.isFavourite ? this.removeFromFavorites() : this.addToFavorites()"
    >
        {{ isFavourite ? this.heartFull : this.heartOutline }}
    </v-icon>
</template>


<script>
export default {
    name: 'favorite-btn',

    data: () => ({
        heartOutline: 'mdi-cards-heart-outline',
        heartFull: 'mdi-cards-heart',
    }),

    props: {
        isFavourite: {
            type: Boolean,
            required: true,
            default: false,
        },
    },

    emits: [
        'add',
        'remove',
    ],

    methods: {
        addToFavorites() {
            this.$emit('add')
        },

        removeFromFavorites() {
            this.$emit('remove')
        },
    },
}
</script>
