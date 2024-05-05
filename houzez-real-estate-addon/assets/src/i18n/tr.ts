import { getClientData } from "../libs/client-data";

export function tr(text: string) {
  const trans = getClientData().clientTranslations[text];
  return trans ?? text;
}
