import cluster from "cluster";
import os from "os";
import express from "express";
import dotenv from "dotenv";
import cors from "cors";
import morgan from "morgan";
import userRoutes from "./routes/userRoutes.js";
import productRoutes from "./routes/productRoutes.js";
import cartRoutes from "./routes/cartRoutes.js";
import orderRoutes from "./routes/orderRoutes.js";

dotenv.config();

const isProduction = process.env.NODE_ENV === "production";

if (cluster.isPrimary && isProduction) {
  const numCPUs = os.cpus().length;
  console.log(`🧠 Master ${process.pid} is running`);
  console.log(`⚙️ Starting ${numCPUs} worker processes...`);
  // Fork workers
  for (let i = 0; i < numCPUs; i++) {
    cluster.fork();
  }
  // Restart workers if they die
  cluster.on("exit", (worker) => {
    console.log(`💀 Worker ${worker.process.pid} died. Restarting...`);
    cluster.fork();
  });
} else {
  const app = express();
  app.use(express.json()); // for JSON payloads
  app.use(express.urlencoded({ extended: true })); // for form data

  const corsOptions = isProduction
    ? { origin: process.env.CLIENT_URL || "https://yourdomain.com" }
    : {};
  app.use(cors(corsOptions));
  app.use(morgan(isProduction ? "combined" : "dev"));

  // Routes
  app.get("/", (req, res) =>
    res.send(
      `✅ User CRUD API Running in ${
        process.env.NODE_ENV || "development"
      } mode...`
    )
  );
  app.use("/api/users", userRoutes);
  app.use("/api/products", productRoutes);
  app.use("/api/carts", cartRoutes);
  app.use("/api/orders", orderRoutes);

  // Port
  const PORT = process.env.PORT || 5000;
  app.listen(PORT, () => {
    console.log(
      `🚀 ${isProduction ? "Worker" : "Dev Server"} ${
        process.pid
      } running → http://localhost:${PORT}`
    );
  });
}
