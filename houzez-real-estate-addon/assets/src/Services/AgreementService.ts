export function convertAgreementToInput(
  inputHtml: string,
  oldInputs?: ConvertedAgreement["inputs"],
): ConvertedAgreement {
  // Define a regex pattern to match the placeholders within double curly braces with hyphens.
  const regex = /(_+){{(.*?)}}(_+)/g;

  // Replace each matched placeholder with an input element and calculate the width.
  const convertedHtml = inputHtml.replace(
    regex,
    (match, leftHyphens, placeholder, rightHyphens) => {
      const inputName = placeholder.trim(); // Remove backslashes for input keys
      const inputWidth = (leftHyphens.length + rightHyphens.length) * 10; // Each hyphen is 10px wide.
      const oldInput = oldInputs ? oldInputs[inputName] : "";
      return `<input class="hre-form-input" type="text" name="${inputName}" value="${oldInput}" style="width:${inputWidth}px;" required />`;
    },
  );

  // Extract the input names and initialize them with empty strings.
  const inputNames: string[] = [];
  inputHtml.replace(regex, (match, leftHyphens, placeholder, rightHyphens) => {
    const inputName = placeholder.trim(); // Remove backslashes for input keys
    inputNames.push(inputName);
    return match;
  });

  const inputs: Record<string, string> = {};
  inputNames.forEach((name) => {
    inputs[name] = "";
  });

  // If there are old inputs, merge them with the new inputs.
  if (oldInputs) {
    Object.keys(oldInputs).forEach((key) => {
      if (!inputs[key]) {
        inputs[key] = oldInputs[key];
      }
    });
  }

  return {
    html: convertedHtml,
    inputs,
  };
}

export function sumNow(a, b) {
  return a + b;
}

export interface ConvertedAgreement {
  html: string;
  inputs: {
    [key: string]: string;
  };
}
