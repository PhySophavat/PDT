<template>
  <!-- EntityBanner removed for cleaner, more modern UI -->
</template>

<script setup>
defineProps({
  name: {
    type: String,
    default: ''
  },
  subtitle: {
    type: String,
    default: ''
  },
  metaId: {
    type: String,
    default: ''
  },
  avatar: {
    type: String,
    default: ''
  }
});

defineEmits(['print', 'download']);
</script>
