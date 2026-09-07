# CarShare Design System

## Color Palette

The landing page uses a warm, natural palette designed to feel trustworthy, social, and energetic.

| Token | Hex / Value | Role |
| --- | --- | --- |
| `--green` | `#173F36` | Primary brand color, hero background, dark buttons, footer |
| `--ink` | `#162522` | Main text, headings, dark button text |
| `--lime` | `#D9F36B` | Primary action buttons, highlights, logo accent, status indicators |
| `--orange` | `#F28D5B` | Secondary accent, labels, CTA sections, emphasis |
| `--paper` | `#FFFEF9` | Main page background and light cards |
| `--cream` | `#F5F5ED` | Feature card backgrounds and soft sections |
| `--mint` | `#C9E8D8` | Supporting pale green accent for future surfaces |
| `--muted` | `#687873` | Secondary text and descriptions |
| `--line` | `rgba(22, 37, 34, 0.14)` | Borders and separators |

## Supporting Colors

These colors are used for small interface details such as avatars, route indicators, and status elements:

- `#4A8A62`: live/status green
- `#B7C9C0`: route connector dots
- `#F1BD99`: avatar peach
- `#C0D8E0`: avatar blue
- `#E4D58F`: avatar yellow
- `#70978A`: avatar sage
- `#F6B58B`: warm notification accent

## Recommended Usage

- Use `--green` with `--lime` for the strongest CarShare brand combination.
- Use `--orange` sparingly for calls to action and important highlights.
- Use `--paper` as the primary light background and `--cream` to separate content areas.
- Use `--ink` for headings and important text; use `--muted` for supporting copy.
- Keep text on `--green` or `--orange` white for contrast.
- Keep text on `--lime` or `--paper` dark using `--ink`.

## CSS Variables

The live variables are defined in [`assets/css/carshare.css`](assets/css/carshare.css):

```css
:root {
  --ink: #162522;
  --muted: #687873;
  --cream: #f5f5ed;
  --paper: #fffef9;
  --mint: #c9e8d8;
  --green: #173f36;
  --lime: #d9f36b;
  --orange: #f28d5b;
  --line: rgba(22, 37, 34, .14);
}
```
