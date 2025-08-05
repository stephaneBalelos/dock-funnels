<template>
  <Editor class="wysiwyg-editor" v-model="model" editorStyle="height: 320px" :modules="quillModules">
    <template v-slot:toolbar>
      <span class="ql-formats">
        <select class="ql-size">
          <option value="small">Klein</option>
          <option value="" selected>Normal</option>
          <option value="large">Groß</option>
          <option value="huge">Sehr Groß</option>
        </select>
        <select class="ql-align">
          <option value="" selected>Links</option>
          <option value="center">Zentriert</option>
          <option value="right">Rechts</option>
          <option value="justify">Blocksatz</option>
        </select>
        <button v-tooltip.bottom="'Bold'" class="ql-bold"></button>
        <button v-tooltip.bottom="'Italic'" class="ql-italic"></button>
        <button v-tooltip.bottom="'Underline'" class="ql-underline"></button>
        <button v-tooltip.bottom="'Strike'" class="ql-strike"></button>
        <button v-tooltip.bottom="'Link'" class="ql-link"></button>
      </span>
    </template>
  </Editor>
</template>

<script setup lang="ts">
import Quill from "quill";
import { Mention, MentionBlot, type MentionBlotData } from "quill-mention";

class StyledMentionBlot extends MentionBlot {
  static render(data: MentionBlotData) {
    const element = document.createElement("span");
    element.innerText = `{${data.value}}`;
    return element;
  }
}
StyledMentionBlot.blotName = "styled-mention";

Quill.register({
  "blots/mention": StyledMentionBlot,
  "modules/mention": Mention,
});

type SearchItem = {
  id: string;
  value: string;
  type?: string;
};

type Props = {
  searchValues: SearchItem[];
};

const model = defineModel<string>({
  default: "",
  type: String,
});

const props = defineProps<Props>();

const quillModules = {
  mention: {
    allowedChars: /^[A-Za-z\sÅÄÖåäö]*$/,
    mentionDenotationChars: ["@"],
    dataAttributes: [
      "id",
      "value",
      "denotationChar",
      "type",
      "link",
      "target",
      "disabled",
    ],
    blotName: "styled-mention",
    source: function (
      searchTerm: string,
      renderList: (values: any[], searchTerm: string) => void,
      _mentionChar: string
    ) {
      let values;

      values = props.searchValues.map((item) => ({
        id: item.id,
        value: item.value,
        type: item.type,
      }));

      if (searchTerm.length === 0) {
        renderList(values, searchTerm);
      } else {
        const matches = [];
        for (let i = 0; i < values.length; i++)
          if (~values[i].value.toLowerCase().indexOf(searchTerm.toLowerCase()))
            matches.push(values[i]);
        renderList(matches, searchTerm);
      }
    },
    renderItem(item: SearchItem, _searchTerm: string) {
      const element = document.createElement("div");
      element.classList.add("p-2", "hover:bg-gray-100", "cursor-pointer");
      element.innerHTML = `${item.value}`;
      return element;
    },
  },
};
</script>

<style>
.wysiwyg-editor {
  width: 100%;
}

.wysiwyg-editor .ql-mention-list-container {
  @apply bg-white border border-gray-300 rounded shadow-lg;
}
</style>
