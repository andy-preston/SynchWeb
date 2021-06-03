<template>
  <div
    class="tw-p-4 tw-rounded tw-border-2"
    :class="classFromLevel(level)"
  >
    <slot />
  </div>
</template>

<script>
export default {
    'name': 'ContextColourCard',
    'props': {
        'level': {
            'type': String,
            'default': '',
        },
    },
    'methods': {
        'classFromLevel': function(level) {
            switch (level) {
                case 'success':
                    return 'tw-border-green-500 tw-bg-green-200'
                case 'info':
                    return 'tw-border-blue-500 tw-bg-blue-200'
                case 'warning':
                    return 'tw-border-yellow-500 tw-bg-yellow-200'
                case 'error':
                    return 'tw-border-red-500 tw-bg-red-200'
                default:
                    return 'tw-border-gray-500 tw-bg-gray-200'
            }
        },
    },
}
</script>
