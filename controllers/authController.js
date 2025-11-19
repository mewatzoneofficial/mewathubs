import bcrypt from "bcrypt";
import jwt from "jsonwebtoken";
import {
  runQuery,
  successResponse,
  errorResponse,
} from "../utils/commonFunctions.js";

export const register = async (req, res) => {
  if (!req.body) {
    return res.status(400).json({ error: "Request body is missing" });
  }

  const { name, email, phone, password } = req.body;

  if (!name || !email || !phone || !password) {
    return errorResponse(res, "All fields are required", 400);
  }

  if (!/^\S+@\S+\.\S+$/.test(email)) {
    return errorResponse(res, "Invalid email format", 400);
  }

  try {
    const [rows] = await runQuery(
      `SELECT id FROM users WHERE email = ?`,
      [email]
    );

    if (rows.length > 0) {
      return errorResponse(res, "Email already registered", 409);
    }

    const hashedPassword = await bcrypt.hash(password, 10);

    const [result] = await runQuery(
      `INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)`,
      [name, email, phone, hashedPassword]
    );

    return successResponse(res, "User registered successfully", {
      id: result.insertId,
      name,
      email,
      phone,
    });
  } catch (err) {
    console.error("Register error:", err.message);
    return errorResponse(res, "Error registering user", 500);
  }
};

export const login = async (req, res) => {
  if (!req.body) {
    return res.status(400).json({ error: "Request body is missing" });
  }

  const { email, password } = req.body;

  if (!email || !password) {
    return errorResponse(res, "Email and password are required", 400);
  }

  try {
    const [rows] = await runQuery(
      `SELECT id, name, email, phone, password FROM users WHERE email = ?`,
      [email]
    );

    if (rows.length === 0) {
      return errorResponse(res, "Invalid email or password", 401);
    }

    const user = rows[0];

    const isMatch = await bcrypt.compare(password, user.password);
    if (!isMatch) {
      return errorResponse(res, "Invalid email or password", 401);
    }

    if (!process.env.JWT_SECRET) {
      throw new Error("JWT_SECRET not configured");
    }

    const token = jwt.sign(
      {
        id: user.id,
        name: user.name,
        email: user.email,
        phone: user.phone,
      },
      process.env.JWT_SECRET,
      { expiresIn: "1d" }
    );

    return successResponse(res, "User logged in successfully", {
      ...user,
      token,
    });
  } catch (err) {
    console.error("Login error:", err.message);
    return errorResponse(res, "Error logging in user", 500);
  }
};



export const location = async (req, res) => {
  if (!req.body) {
    return res.status(400).json({ error: "Request body is missing" });
  }
  const { lat, lng } = req.body;
  if (lat === undefined || lng === undefined) {
    return errorResponse(res, "Latitude and longitude are required", 400);
  }

  try {
  const nominatimRes = await fetch(
    `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&addressdetails=1`,
    { headers: { "User-Agent": "chat-app" } }
  );
  const nominatimData = await nominatimRes.json();
  const tzRes = await fetch(
    `http://api.geonames.org/timezoneJSON?lat=${lat}&lng=${lng}&username=demo`
  );
  const tzData = await tzRes.json();
  const overpassRes = await fetch(
    `https://overpass-api.de/api/interpreter?data=[out:json];(node(around:1000,${lat},${lng})["tourism"];);out;`
  );
  const overpassData = await overpassRes.json();
  const landmarks = overpassData.elements
    .map((el) => el.tags && el.tags.name)
    .filter(Boolean)
    .slice(0, 5);

   const location = {
      lat,
      lng,
      city:
        nominatimData.address.city ||
        nominatimData.address.town ||
        nominatimData.address.village,
      district: nominatimData.address.county || null,
      state: nominatimData.address.state,
      state_code: nominatimData.address.state_code || null,
      country: nominatimData.address.country,
      country_code: nominatimData.address.country_code?.toUpperCase(),
      postcode: nominatimData.address.postcode,
      address: nominatimData.display_name,
      timezone: tzData.timezoneId || null,
      continent: "Asia", // static, can map later
      location_type: "urban",
      nearby_landmarks: landmarks,
      coordinates_format: toDMS(lat, lng),
    }

    return successResponse(res, "User Location in successfully", {...location});
  } catch (err) {
    console.error("Login error:", err.message);
    return errorResponse(res, "Error logging in user", 500);
  }
};


// Convert decimal to DMS
function toDMS(lat, lng) {
  const toDegMinSec = (coord, isLat) => {
    const absolute = Math.abs(coord);
    const degrees = Math.floor(absolute);
    const minutesNotTruncated = (absolute - degrees) * 60;
    const minutes = Math.floor(minutesNotTruncated);
    const seconds = Math.floor((minutesNotTruncated - minutes) * 60);
    const direction = coord >= 0 ? (isLat ? "N" : "E") : isLat ? "S" : "W";
    return `${degrees}°${minutes}'${seconds}"${direction}`;
  };
  return {
    decimal: `${lat}, ${lng}`,
    dms: `${toDegMinSec(lat, true)} ${toDegMinSec(lng, false)}`,
  };
}