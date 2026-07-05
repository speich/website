import { rmSync, mkdirSync } from "node:fs";
import { join, dirname } from "node:path";

console.log("🚀 Starting Native Bun Build Pipeline...");

// =========================================================
// HELPER: Replaces `copyfiles -u <stripCount> -e <ignore>`
// =========================================================
async function copy(pattern, outDir, stripCount = 0, ignorePaths = []) {
    const glob = new Bun.Glob(pattern);
    for await (const file of glob.scan()) {
        // Skip ignored directories (like /tests/, /demos/)
        if (ignorePaths.some(ignore => file.includes(ignore))) continue;

        // Strip the leading directories (e.g., node_modules/dojo/ -> dojo/)
        const parts = file.split('/');
        const strippedPath = parts.slice(stripCount).join('/');
        const dest = join(outDir, strippedPath);

        // Ensure the directory exists, then copy the file natively
        mkdirSync(dirname(dest), { recursive: true });
        await Bun.write(dest, Bun.file(file));
    }
}

// =========================================================
// 1. CLEANUP (Replaces rimraf)
// =========================================================
console.log("🧹 Cleaning old directories...");
const dirsToClean = [
    "dgrid", "dojo", "jquery", "photoswipe", "prismjs", "put-selector", "xstyle", "speich.net/DialogConfirm"
];

for (const dir of dirsToClean) {
    rmSync(dir, { recursive: true, force: true });
}

// =========================================================
// 2. COPY ASSETS (Replaces copyfiles)
// =========================================================
console.log("📂 Copying and restructuring node_modules...");

await Promise.all([
    // Custom single file copies
    Bun.write("speich.net/DialogConfirm/DialogConfirm.js", Bun.file("node_modules/dialog-confirm/DialogConfirm.js")),
    Bun.write("tinyamd.min.js", Bun.file("node_modules/tinyamd/tinyamd.min.js")),
    Bun.write("put-selector/LICENSE", Bun.file("node_modules/put-selector/LICENSE")),
    Bun.write("put-selector/package.json", Bun.file("node_modules/put-selector/package.json")),
    Bun.write("put-selector/put.js", Bun.file("node_modules/put-selector/put.js")),

    // Deep directory copies with stripping and exclusions
    copy("node_modules/dgrid/**/*", "dgrid", 2, ["/test/", "/demos/", "/doc/"]),
    copy("node_modules/dojo/**/*", "dojo/dojo", 2, ["/tests/", "/testsDOH/"]),
    copy("node_modules/dijit/**/*", "dojo/dijit", 2, ["/tests/"]),
    copy("node_modules/dojox/**/*", "dojo/dojox", 2, ["/tests/"]),
    copy("node_modules/jquery/dist/*", "jquery", 3),
    copy("node_modules/photoswipe/dist/**/*", "photoswipe", 3),
    copy("node_modules/prismjs/**/*", "prismjs", 2),
    copy("node_modules/xstyle/**/*", "xstyle", 2, ["/build/"])
]);

// =========================================================
// 3. MINIFY JS & CSS (Replaces UglifyJS and CSSO)
// =========================================================
console.log("🗜️ Minifying JavaScript and CSS...");

// Gather all JS files
const rawJsFiles = [
    ...new Bun.Glob("speich.net/DialogConfirm/**/*.js").scanSync(),
    ...new Bun.Glob("dgrid/**/*.js").scanSync(),
    ...new Bun.Glob("dojo/**/*.js").scanSync(),
    ...new Bun.Glob("xstyle/**/*.js").scanSync(),
    "prismjs/prism.js",
    "put-selector/put.js"
];

// FILTER: Exclude Dojo's internal Node CLI tools and theme compilers
// that confuse modern AST bundlers
const jsFiles = rawJsFiles.filter(file =>
    !file.endsWith("compile.js") &&
    !file.endsWith("configNode.js")
);

// Gather CSS files
const cssFiles = [
    "dgrid/css/dgrid.css",
    "photoswipe/photoswipe.css",
    "prismjs/themes/prism.css"
];

// Execute the Bun native bundler
await Bun.build({
    entrypoints: [...jsFiles, ...cssFiles],
    outdir: "./",
    root: "./",
    minify: true,
    sourcemap: "none",
    // CRITICAL: Tells Bun to completely ignore all require() and import statements.
    // This forces Bun to act like a "dumb" minifier instead of a bundler.
    external: ["*"]
});

console.log("✅ Build Complete!d");