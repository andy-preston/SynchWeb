<template>
    <notification-card v-if="!closed && motd && motd.message" :notification="motd">
        <i slot="actions" class="fa fa-times" @click="closed = true"></i>
    </notification-card>
    <!-- Spacer for cases we have no message of the day -->
    <div v-else class="tw-mb-4"></div>
</template>

<script>

import NotificationCard from "app/components/notification-card.vue"

export default {
    "name": "Motd",
    "components": {
        "notification-card": NotificationCard,
    },
    "props":  {
        "motd": Object,
    },
    "data": function() {
        return {
            "closed": false,
        }
    },
}
</script>