<template>
  <notification-card
    v-if="!closed && motd && motd.message"
    class="tw-mt-8"
    :notification="motd"
  >
    <i
      slot="actions"
      class="fa fa-times"
      @click="closed = true"
    />
  </notification-card>
  <!-- Spacer for cases we have no message of the day -->
  <div
    v-else
    class="tw-mb-4"
  />
</template>

<script>

import NotificationCard from 'app/components/notification-card.vue'

export default {
    'name': 'Motd',
    'components': {
        'notification-card': NotificationCard,
    },
    'props':  {
        'motd': {
            'type': Object,
            // TODO: is there any way we could use a model design-pattern
            // to deal with common data object "classes" across components?
            'default': { // see notification-card for details
                'level': '',
                'message': '',
                'title': '',
            },
        },
    },
    'data': function() {
        return {
            'closed': false,
        }
    },
}
</script>