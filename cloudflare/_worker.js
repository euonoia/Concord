export default {
  async scheduled(event, env, ctx) {
    const url = "https://concord-03cc.onrender.com/"; // Replace with your Render URL
    await fetch(url);
    console.log(`Pinged: ${url}`);
  },
};