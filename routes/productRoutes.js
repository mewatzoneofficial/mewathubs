import express from "express";
import { makeUploader } from "../middlewares/multer.js";
import {
  getAllRecords,
  createRecord,
  getRecordById,
  updateRecord,
  deleteRecord,
} from "../controllers/productController.js";

const router = express.Router();
const upload = makeUploader("products");

router.get("/", getAllRecords);
router.post("/", upload.single("image"), createRecord);
router.get("/:id", getRecordById);
router.put("/:id", updateRecord);
router.delete("/:id", deleteRecord);

export default router;

