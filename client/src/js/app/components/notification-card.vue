<!--
This component displays notifications - styling the border and background based on the message level
It is designed to work with transient notifications and for a persistent notification that may contain a link
It uses slots for title and header so it can be overridden by a parent component
-->
<template>
  <context-colour-card
    :level="notification.level"
    class="tw-hidden md:tw-flex tw-justify-between"
  >
    <div class="tw-flex">
      <slot name="title">
        <p
          v-if="notification.title"
          class="tw-font-bold"
        >
          {{ notification.title }}
        </p>
      </slot>
      <slot name="message">
        <!--
            https://vuejs.org/v2/api/#v-html recommends against using v-html
            consider refactoring
        -->
        <p
          class="tw-pl-2"
          v-html="notification.message"
        />
      </slot>
    </div>
    <div class="tw-flex">
      <!-- Placeholder for any buttons, icons etc. -->
      <slot name="actions" />
    </div>
  </context-colour-card>
</template>

<script>
import contextColourCard from './context-colour-card.vue'

export default {
    'name': 'NotificationCard',
    'components': {
        contextColourCard
    },
    'filters': {
        // Simple filter to convert text to upper case
        'upper': function (value) {
            if (!value) {
                return ''
            } else {
                return value.toString().toUpperCase()
            }
        }
    },
    'props': {
        'notification': {
            'type': Object, // with level, message, title properties
            'required': true,
        }
    },
}
</script>
