<template>
    <router-link to="/favorite" class="text-decoration-none mx-2">

        <!-- Filled heart with number of favorite lectures -->

        <v-badge
            v-if="hasFavoritedLectures"
            :content="badgeContent"
            :max="99"
            color="error"
        >
            <v-icon size="x-large" color="white">
                mdi-cards-heart
            </v-icon>
        </v-badge>


        <!-- Outline heart without number of favorite lectures -->

        <v-icon
            v-else
            size="x-large" color="white"
        >
            mdi-cards-heart-outline
        </v-icon>

    </router-link>
</template>


<script>
export default {
    name: 'app-bar-favorite',

    computed: {
        badgeContent() {
            return this.$store.getters['favorite/favoritedLecturesId'].length
        },

        hasFavoritedLectures() {
            return this.$store.getters['favorite/hasFavoritedLectures']
        },
    },
}
</script>
