import {
  convertAgreementToInput,
  ConvertedAgreement,
  sumNow,
} from "./AgreementService";

describe("Transform __{{Some text}}__ in html to inputs", () => {
  test("Converts <p>____{{Some Text}}____</p><p>And then ________{{Longer Input}}__________ <span>Then for 2 __{{Just Two}}__</span></p> to input", () => {
    expect(
      convertAgreementToInput(
        "<p>____{{Some Text}}____</p><p>And then ________{{Longer Input}}__________ <span>Then for 2 __{{Just Two}}__</span></p>",
      ),
    ).toStrictEqual({
      html: '<p><input class="hre-form-input" type="text" name="Some Text" value="" style="width:80px;" required /></p><p>And then <input class="hre-form-input" type="text" name="Longer Input" value="" style="width:180px;" required /> <span>Then for 2 <input class="hre-form-input" type="text" name="Just Two" value="" style="width:40px;" required /></span></p>',
      inputs: {
        "Some Text": "",
        "Longer Input": "",
        "Just Two": "",
      },
    } as ConvertedAgreement);
  });
});

describe("Transform __{{Some text}}__ in html to inputs with old inputs", () => {
  test("Converts <p>____{{Some Text}}____</p><p>And then ________{{Longer Input}}__________ <span>Then for 2 __{{Just Two}}__</span></p> to input", () => {
    expect(
      convertAgreementToInput(
        "<p>____{{Some Text}}____</p><p>And then ________{{Longer Input}}__________ <span>Then for 2 __{{Just Two}}__</span></p>",
        {
          "Some Text": "Random Text",
          "Longer Input": "Hey, this is a longer input",
          "Just Two": "Just another input",
        },
      ),
    ).toStrictEqual({
      html: '<p><input class="hre-form-input" type="text" name="Some Text" value="Random Text" style="width:80px;" required /></p><p>And then <input class="hre-form-input" type="text" name="Longer Input" value="Hey, this is a longer input" style="width:180px;" required /> <span>Then for 2 <input class="hre-form-input" type="text" name="Just Two" value="Just another input" style="width:40px;" required /></span></p>',
      inputs: {
        "Some Text": "Random Text",
        "Longer Input": "Hey, this is a longer input",
        "Just Two": "Just another input",
      },
    } as ConvertedAgreement);
  });
});
