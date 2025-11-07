import multer from "multer";
import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";
import { dirname } from "path";

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const baseUploadPath = path.join(__dirname, "../uploads");

// 🧩 Create base uploads folder if missing
if (!fs.existsSync(baseUploadPath)) {
  fs.mkdirSync(baseUploadPath);
  console.log("📁 Created base uploads folder");
} else {
  console.log("✅ Base uploads folder already exists");
}

// 🧰 Custom uploader factory
export const makeUploader = (folderName) => {
  // Prevent directory traversal attacks
  const safeFolder = path.normalize(folderName).replace(/^(\.\.[/\\])+/, "");
  const uploadPath = path.join(baseUploadPath, safeFolder);

  // 🧱 Check and create subfolder only if missing
  if (!fs.existsSync(uploadPath)) {
    fs.mkdirSync(uploadPath, { recursive: true });
    console.log(`📁 Created subfolder: ${uploadPath}`);
  } else {
    console.log(`✅ Subfolder already exists: ${uploadPath}`);
  }

  // 🗂️ Configure Multer storage
  const storage = multer.diskStorage({
    destination: (req, file, cb) => cb(null, uploadPath),
    filename: (req, file, cb) => {
      const ext = path.extname(file.originalname);
      cb(null, `${file.fieldname}-${Date.now()}${ext}`);
    },
  });

  return multer({ storage });
};
