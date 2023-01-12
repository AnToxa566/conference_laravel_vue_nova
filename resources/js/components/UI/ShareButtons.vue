<template>
    <ShareNetwork
        network="facebook"
        :url="this.share_url"
        title="Confy"
        :description="this.share_text"
        hashtags="bwt,quest"
        class="text-decoration-none"
    >
        <v-icon class="mx-2" color="blue-darken-2" size="x-large" icon="mdi-facebook"/>
    </ShareNetwork>

    <ShareNetwork
        network="twitter"
        :url="this.share_url"
        :title="this.share_text"
        hashtags="bwt,quest"
        class="text-decoration-none"
    >
        <v-icon class="mx-2" color="blue-darken-2" size="x-large" icon="mdi-twitter"/>
    </ShareNetwork>
</template>


<script>
export default {
    name: 'share-buttons',

    data: () => ({
        share_url: import.meta.env.VITE_SHARE_URI,
        share_text: import.meta.env.VITE_SHARE_TEXT,
    })
}
</script>
