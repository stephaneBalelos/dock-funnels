<template>
  <div>
    <slot :closeDialog="closeDialog" :openDialog="openDialog">
      <Button label="Open Dialog" @click="openDialog" size="small" />
    </slot>
    <Dialog
      v-model:visible="visible"
      modal
      header="Formular Einstellungen"
      :style="{ width: '100%', maxWidth: '800px' }"
      :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
      class="dock-funnels-root"
    >
      <Tabs value="0">
        <TabList>
          <Tab value="0">Allgemein Einstellungen</Tab>
          <Tab value="1">E-Mail Benachrichtigung</Tab>
          <Tab value="2">SMTP Einstellungen</Tab>
        </TabList>
        <TabPanels>
          <TabPanel value="0">
            <GeneralSettingsTab />
          </TabPanel>
          <TabPanel value="1">
            <NotificationsSettingsTab />
          </TabPanel>
          <TabPanel value="2">
            <EmailSettingTab />
          </TabPanel>
        </TabPanels>
      </Tabs>
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import GeneralSettingsTab from "./GeneralSettingsTab.vue";
import EmailSettingTab from "./SmtpSettingTab.vue";
import NotificationsSettingsTab from "./NotificationsSettingsTab.vue";

const visible = ref(false);

const openDialog = () => {
  visible.value = true;
};
const closeDialog = () => {
  visible.value = false;
};
</script>
