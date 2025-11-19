import express from "express";
import { 
    register, 
    login,
    location
} from "../controllers/authController.js";

const router = express.Router();

router.post("/register", register);
router.post("/login", login);
router.post("/location", location);

export default router;
