import skipFormatting from '@vue/eslint-config-prettier/skip-formatting'
import vueTsEslintConfig from '@vue/eslint-config-typescript'
import pluginImport from 'eslint-plugin-import'
import pluginVue from 'eslint-plugin-vue'

export default [
  {
    name: 'app/files-to-lint',
    files: ['**/*.{ts,mts,tsx,vue}'],
  },

  {
    name: 'app/files-to-ignore',
    ignores: ['**/dist/**', '**/dist-ssr/**', '**/coverage/**'],
  },

  ...pluginVue.configs['flat/essential'],
  ...vueTsEslintConfig(),
  skipFormatting,
  {
    plugins: {
      import: pluginImport,
    },
    rules: {
      'vue/multi-word-component-names': 'off',

      // Automatically sort imports
      'import/order': [
        'error',
        {
          groups: [
            ['builtin', 'external'], // Node.js built-ins & external packages
            'internal', // Internal project imports
            ['parent', 'sibling', 'index'], // Parent & sibling imports
          ],
          pathGroups: [
            {
              pattern: '@/**', // Aliases (e.g., @/components, @/utils)
              group: 'internal',
              position: 'before',
            },
          ],
          pathGroupsExcludedImportTypes: ['builtin'],
          'newlines-between': 'always',
          alphabetize: {
            order: 'asc', // Sort alphabetically
            caseInsensitive: true, // Ignore case
          },
        },
      ],

      // Enforce import sorting automatically
      'sort-imports': [
        'error',
        {
          ignoreCase: true,
          ignoreDeclarationSort: true,
          allowSeparatedGroups: true,
        },
      ],
    },
  },
]
