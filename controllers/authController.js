import bcrypt from "bcrypt";
import jwt from "jsonwebtoken";
import {
  runQuery,
  successResponse,
  errorResponse,
} from "../utils/commonFunctions.js";

export const register = async (req, res) => {
  console.log("req.body:", req.body);

  if (!req.body) {
    return res.status(400).json({ error: "Request body is missing" });
  }

  const { name, email, mobile, password } = req.body;

  if (!name || !email || !mobile || !password) {
    return errorResponse(
      res,
      "All fields (name, email, mobile, password) are required",
      400
    );
  }

  try {
    const [existingUser] = await runQuery(
      `SELECT * FROM faculity_users WHERE email = ?`,
      [email]
    );

    if (existingUser.length > 0) {
      return errorResponse(res, "Email already registered", 409);
    }

    const salt = await bcrypt.genSalt(10);
    const hashedPassword = await bcrypt.hash(password, salt);

    // 3️⃣ Insert new user into database
    const [result] = await runQuery(
      `INSERT INTO faculity_users (name, email, mobile, password) VALUES (?, ?, ?, ?)`,
      [name, email, mobile, hashedPassword]
    );

    const newUserId = result.insertId;
    return successResponse(res, "User registered successfully", {
      FacultyID: newUserId,
      name,
      email,
      mobile,
    });
  } catch (err) {
    console.error("Error registering user:", err);
    return errorResponse(res, "Error registering user", 500);
  }
};

export const login = async (req, res) => {
  console.log("req.body:", req.body);

  if (!req.body) {
    return res.status(400).json({ error: "Request body is missing" });
  }

  const { email, password } = req.body;
  if (!email || !password) {
    return errorResponse(res, "Email and password are required", 400);
  }

  try {
    const [user] = await runQuery(
      `SELECT faculityID, name,  email, mobile, password FROM faculity_users WHERE email = ?`,
      [email]
    );

    if (!user || user.length === 0) {
      return errorResponse(res, "Invalid email", 401);
    }

    const existingUser = user[0];

    let hashedPassword = existingUser.password;
    if (hashedPassword.startsWith("$2y$")) {
      hashedPassword = hashedPassword.replace("$2y$", "$2a$");
    }

    const isMatch = await bcrypt.compare(password, hashedPassword);
    if (!isMatch) {
      return errorResponse(res, "Invalid email or password", 401);
    }

    const token = jwt.sign(
      {
        id: existingUser.faculityID,
        name: existingUser.name,
        email: existingUser.email,
        mobile: existingUser.mobile,
      },
      process.env.JWT_SECRET,
      { expiresIn: "1d" }
    );

    // 4️⃣ Respond with success
    return successResponse(res, "User logged in successfully", {
      FacultyID: existingUser.faculityID,
      name: existingUser.name,
      email: existingUser.email,
      mobile: existingUser.mobile,
      token,
    });
  } catch (err) {
    console.error("Error logging in:", err);
    return errorResponse(res, "Error logging in user", 500);
  }
};
