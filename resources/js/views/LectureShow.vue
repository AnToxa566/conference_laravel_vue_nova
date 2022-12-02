<template>
    <my-header>
        <template v-slot:header>Lecture details</template>
    </my-header>
</template>


<script>
export default {
    data() {
        return {
            conference_id: null,
            lecture_id: null,
        };
    },

    created() {
        this.conference_id = parseInt(this.$route.params.conference_id, 10);
        this.lecture_id = parseInt(this.$route.params.lecture_id, 10);
    },
}
</script>


<style scoped>

</style>
