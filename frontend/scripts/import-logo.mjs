import { readFile, writeFile } from "node:fs/promises";
import { fileURLToPath } from "node:url";

const configPath = fileURLToPath(
  new URL("../src/config/brand.json", import.meta.url),
);
const destination = fileURLToPath(
  new URL("../public/images/facebook-logo.jpg", import.meta.url),
);
const config = JSON.parse(await readFile(configPath, "utf8"));
const fileOption = process.argv.indexOf("--file");

try {
  let image;
  if (fileOption !== -1) {
    const path = process.argv[fileOption + 1];
    if (!path) throw new Error("Provide a JPEG path after --file.");
    image = await readFile(path);
  } else {
    const response = await fetch(config.source, {
      signal: AbortSignal.timeout(15000),
    });
    if (!response.ok)
      throw new Error(`The image host returned HTTP ${response.status}.`);
    image = Buffer.from(await response.arrayBuffer());
  }
  if (image.length < 100 || image[0] !== 0xff || image[1] !== 0xd8) {
    throw new Error("The supplied file is not a JPEG image.");
  }
  await writeFile(destination, image);
  await writeFile(
    configPath,
    `${JSON.stringify({ ...config, logo: "/images/facebook-logo.jpg" }, null, 2)}\n`,
  );
  process.stdout.write(
    "Imported the original logo into public/images/facebook-logo.jpg.\n",
  );
} catch (error) {
  process.stderr.write(
    `Logo import failed: ${error.message}\nThe current logo configuration was kept.\n`,
  );
  process.exitCode = 1;
}
