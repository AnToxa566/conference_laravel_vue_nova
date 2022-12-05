<template>
    <div>
        {{ this.comment.id }}
    </div>
</template>


<script>
export default {
    props: {
        comment: {
            type: Object,
            required: true,
        },
    },
}
</script>


<style scoped>

</style>
