import type { FormState } from "@/types";
import { definePreset, palette, updatePreset } from "@primeuix/themes";
import Aura from '@primeuix/themes/aura';

export const slugify = (text: string): string => {
  // Convert the input text to a slug format
  // Slug format: lowercase, spaces replaced with unsderscores, non-word characters removed, and trimmed, special characters like hyphens and umlauts handled
  return text
    .toString()
    .toLowerCase()
    .replace(/\s+/g, '_') // Replace spaces with underscores
    .replace(/--+/g, '_') // Replace multiple hyphens
    .replace(/^-+/, '') // Trim hyphens from the start
    .replace(/-+$/, '') // Trim hyphens from the end
    .replace(/ä/g, 'ae') // Replace umlaut 'ä' with 'ae'
    .replace(/ö/g, 'oe') // Replace umlaut 'ö' with 'oe'
    .replace(/ü/g, 'ue') // Replace umlaut 'ü' with 'ue'
    .replace(/ß/g, 'ss') // Replace sharp s 'ß' with 'ss'
    .replace(/[^\w\-]+/g, '') // Remove all non-word characters except hyphens
    .replace(/[^a-z0-9\-]/g, '') // Remove any remaining non-alphanumeric characters except hyphens
    .trim(); // Trim whitespace

}

export const FormTestData: FormState = {
  id: 1,
  title: "Online Terminvereinbarung [Test]",
  description:
    "Bitte füllen Sie das Formular aus, um einen Termin zu vereinbaren.",
  outro_settings: {
    title: "Vielen Dank für Ihre Terminvereinbarung!",
    description:
      "Wir haben Ihre Anfrage erhalten und werden uns in Kürze mit Ihnen in Verbindung setzen.",
    button_text: "Zurück zur Startseite",
    button_url: "https://dock26.de", // Optional URL for the button in the outro
  },
  form_steps: [
    {
      title: "Wählen Sie Ihre Fachrichtung",
      description: "Wählen Sie bitte eine Fachrichtung aus.",
    },
    {
      title: "Wählen Sie Ihre Beschwerde",
      description:
        "Bitte wählen Sie eine Beschwerde in der Fachrichtung Orthopädie aus.",
    },
    {
      title: "Haben sie alle Erforderlichen Dokumente bereit?",
      description:
        "Bitte stellen Sie sicher, dass Sie alle erforderlichen Dokumente bereit haben.",
    },
    {
      title: "Zusammenfassung und Abschluss",
      description:
        "Überprüfen Sie Ihre Angaben und schließen Sie die Terminvereinbarung ab.",
    },
  ],
  form_fields: [
    {
      step_index: 0,
      label: "Intro HTML",
      description: "Bitte geben Sie Ihre E-Mail-Adresse ein.",
      field_name: "intro_html",
      type: "customHtml",
      required: false,
      html_content: `<div class="custom-html-field"><p><span class="ql-size-huge">Sehr&nbsp;Groß</span></p><p><span class="ql-size-large">Groß</span></p><p>normal</p><p><span class="ql-size-small">klein</span></p><p></p><p><a href="https://google.com" rel="noopener noreferrer" target="_blank">Verlinkung</a></p></div>`,
    },
    {
      step_index: 0,
      label: "Vorname",
      description: "Bitte geben Sie Ihren Vornamen ein.",
      field_name: "vorname",
      type: "text",
      input_type: "text",
      placeholder: "Max",
      required: true,
      default_value: "",
    },
    {
      step_index: 0,
      label: "Fahrichtung",
      description: "Fahrrichtung auswählen",
      field_name: "fachrichtung",
      default_value: "orthopedie",
      type: "select",
      field_settings: {
        hide_label: false,
        align: 'start', // stretch, left, center, right
        text_align: 'start', // start, center, end
      },
      options: [
        {
          value: "orthopedie",
          label: "Orthopädie",
          description: "Beschreibung für Orthopädie",
          depends_on: [],
        },
        {
          value: "viszeralchirurgie",
          label: "Viszeralchirurgie",
          description: "Beschreibung für Viszeralchirurgie",
          depends_on: [],
        },
        {
          value: "handchirurgie",
          label: "Handchirurgie",
          description: "Beschreibung für Handchirurgie",
          depends_on: [],
        },
      ],
      required: true,
    },
    {
      step_index: 1,
      label: "Dependent on Fahrrichtung Orthopädie",
      field_name: "orthopedie_beschwerde",
      description: "Bitte wählen Sie eine spezifische Beschwerde aus.",
      type: "select",
      options: [
        {
          value: "schulter",
          label: "Schulter",
          description: "Schulterbeschwerden und -verletzungen",
          depends_on: [{
            field_name: "fachrichtung",
            value: "orthopedie",
          }]
        },
        {
          value: "arm",
          label: "Arm",
          description: "Armbeschwerden und -verletzungen",
          depends_on: []
        },
        {
          value: "huefte",
          label: "Hüfte",
          description: "Hüftbeschwerden und -verletzungen",
          depends_on: []
        },
        {
          value: "fueße",
          label: "Füße",
          description: "Fußbeschwerden und -verletzungen",
          depends_on: []
        },
      ],
      required: true,
      depends_on: [],
    },
    {
      step_index: 1,
      label: "Dependent on Fahrrichtung Viszeralchirurgie",
      field_name: "viszeralchirurgie_beschwerde",
      description: "Bitte wählen Sie eine spezifische Beschwerde aus.",
      type: "select",
      options: [
        {
          value: "tumor",
          label: "Tumor",
          description: "Tumorbehandlung und -diagnose",
          depends_on: [],
        },
        {
          value: "schilddruesenprobleme",
          label: "Schilddrüsenprobleme",
          description: "Schilddrüsenprobleme und -behandlungen",
          depends_on: [],
        },
      ],
      required: true,
      depends_on: [
        {
          field_name: "fachrichtung",
          value: "viszeralchirurgie",
        },
      ],
    },
    {
      step_index: 2,
      label: "Dokumente",
      field_name: "dokumente",
      description:
        "Bitte stellen Sie sicher, dass Sie alle erforderlichen Dokumente bereit haben.",
      type: "checkboxList",
      min: 2,
      max: 2,
      options: [
        {
          value: "roentgenbilder",
          label: "Röntgenbilder",
          is_required: true,
          description: "Röntgenbilder der betroffenen Gelenke",
          depends_on: [
            {
              field_name: "fachrichtung",
              value: "orthopedie",
            },
            {
              field_name: "orthopedie_beschwerde",
              value: "schulter",
            }
          ],
        },
        {
          value: "arztberichte",
          label: "Arztberichte",
          is_required: true,
          description: "Arztberichte und medizinische Gutachten",
          depends_on: [
            {
              field_name: "fachrichtung",
              value: "viszeralchirurgie",
            },
          ],
        },
        {
          value: "vorherige behandlungen",
          label: "Vorherige Behandlungen",
          description:
            "Informationen zu vorherigen Behandlungen oder Operationen",
          depends_on: []
        },

      ],
      required: true,
    },
    {
      step_index: 2,
      label: "Zusätzliche Informationen",
      field_name: "zusatzinfo",
      description: "Geben Sie hier zusätzliche Informationen an.",
      type: "text",
      input_type: "date",
      placeholder: "TT.MM.JJJJ",
      required: true,
      default_value: "",
    },
    {
      step_index: 2,
      label: "Zustimmung zur Datenverarbeitung",
      field_name: "datenschutz",
      description: "Bitte stimmen Sie der Datenverarbeitung zu.",
      type: "checkboxList",
      options: [
        {
          value: "datenschutz",
          label: "Ich stimme der Verarbeitung meiner Daten zu.",
          description:
            "Ich habe die Datenschutzerklärung gelesen und akzeptiere sie.",
          depends_on: [],
        },
      ],
      required: true,
      min: 1,
      max: 1,
    },
    {
      step_index: 3,
      label: "Zusammenfassung",
      field_name: "zusammenfassung",
      description:
        "Überprüfen Sie Ihre Angaben und schließen Sie die Terminvereinbarung ab.",
      type: "submissionSummary",
      required: false,
      show_full_summary: true,
    },
  ],
  form_settings: {
    design_settings: {
      colors: {
        primary: "#98473E", // Primary color for the form
        surface: "#64748b", // Secondary color for the form
      },
      header: {
        show: true, // Whether to show the header
        align: "center", // Alignment of the header
      },
      steps: {
        hide_step_header: false, // Whether to show the steps header
        text_align: "text-left", // Text alignment for steps
        items_align: "items-start", // Alignment of step items
        step_transition: "slide", // Transition effect for steps
      },
      footer: {
        show_progress_bar: true, // Whether to show the progress bar in the footer
      },
    },
    notifications_settings: {
      emails: "",
      subject: "Neue Terminvereinbarung",
      body: "Sie haben eine neue Terminvereinbarung erhalten.",
    },
    onSubmitAction: [],
    smtp_settings: {
      host: "",
      port: 587,
      username: "",
      password: "",
      encryption: "tls",
      from_email: "",
      from_name: "",
      enabled: false,
      reply_to: "",
    },
  }
}

export const configureTheme = (primaryColor: string, surfaceColor: string) => {
  const customPrimary = palette(primaryColor)
  const customSurface = palette(surfaceColor)

  // Set css variables for the theme
  document.documentElement.style.setProperty('--dock-funnels-color-primary-50', customPrimary['50'])
  document.documentElement.style.setProperty('--dock-funnels-color-primary-100', customPrimary['100'])
  document.documentElement.style.setProperty('--dock-funnels-color-primary-200', customPrimary['200'])
  document.documentElement.style.setProperty('--dock-funnels-color-primary-300', customPrimary['300'])
  document.documentElement.style.setProperty('--dock-funnels-color-primary-400', customPrimary['400'])
  document.documentElement.style.setProperty('--dock-funnels-color-primary-500', customPrimary['500'])
  document.documentElement.style.setProperty('--dock-funnels-color-primary-600', customPrimary['600'])
  document.documentElement.style.setProperty('--dock-funnels-color-primary-700', customPrimary['700'])
  document.documentElement.style.setProperty('--dock-funnels-color-primary-800', customPrimary['800'])
  document.documentElement.style.setProperty('--dock-funnels-color-primary-900', customPrimary['900'])
  document.documentElement.style.setProperty('--dock-funnels-color-primary-950', customPrimary['950'])

  document.documentElement.style.setProperty('--dock-funnels-color-surface-50', customSurface['50'])
  document.documentElement.style.setProperty('--dock-funnels-color-surface-100', customSurface['100'])
  document.documentElement.style.setProperty('--dock-funnels-color-surface-200', customSurface['200'])
  document.documentElement.style.setProperty('--dock-funnels-color-surface-300', customSurface['300'])
  document.documentElement.style.setProperty('--dock-funnels-color-surface-400', customSurface['400'])
  document.documentElement.style.setProperty('--dock-funnels-color-surface-500', customSurface['500'])
  document.documentElement.style.setProperty('--dock-funnels-color-surface-600', customSurface['600'])
  document.documentElement.style.setProperty('--dock-funnels-color-surface-700', customSurface['700'])
  document.documentElement.style.setProperty('--dock-funnels-color-surface-800', customSurface['800'])
  document.documentElement.style.setProperty('--dock-funnels-color-surface-900', customSurface['900'])
  document.documentElement.style.setProperty('--dock-funnels-color-surface-950', customSurface['950'])
}

export const updateThemePreset = (primaryColor: string, surfaceColor: string) => {
    const customPrimary = palette(primaryColor)
  const customSurface = palette(surfaceColor)
  const preset = definePreset(Aura, {
    semantic: {
      primary: {
        50: customPrimary[50],
        100: customPrimary[100],
        200: customPrimary[200],
        300: customPrimary[300],
        400: customPrimary[400],
        500: customPrimary[500],
        600: customPrimary[600],
        700: customPrimary[700],
        800: customPrimary[800],
        900: customPrimary[900],
        950: customPrimary[950],
      },
      colorScheme: {
        light: {
          surface: {
            50: customSurface[50],
            100: customSurface[100],
            200: customSurface[200],
            300: customSurface[300],
            400: customSurface[400],
            500: customSurface[500],
            600: customSurface[600],
            700: customSurface[700],
            800: customSurface[800],
            900: customSurface[900],
            950: customSurface[950],
          }
        },
        dark: {
          surface: {
            50: customSurface[50],
            100: customSurface[100],
            200: customSurface[200],
            300: customSurface[300],
            400: customSurface[400],
            500: customSurface[500],
            600: customSurface[600],
            700: customSurface[700],
            800: customSurface[800],
            900: customSurface[900],
            950: customSurface[950],
          }
        }
      }
    },
  })

  updatePreset(preset);
}