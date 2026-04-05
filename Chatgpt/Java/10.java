import com.sun.net.httpserver.HttpExchange;
import com.sun.net.httpserver.HttpServer;
import java.io.OutputStream;
import java.net.InetSocketAddress;
import java.nio.charset.StandardCharsets;
import java.util.Map;
import java.util.function.Supplier;

public class SafePluginApi {
    public static void main(String[] args) throws Exception {
        Map<String, Supplier<String>> handlers = Map.of(
            "hello", () -> "Hello from HelloHandler",
            "status", () -> "Status: OK",
            "version", () -> "API version 1.0"
        );

        HttpServer server = HttpServer.create(new InetSocketAddress(8080), 0);
        server.createContext("/run", exchange -> {
            String query = exchange.getRequestURI().getQuery();
            String name = getParam(query, "handler");

            String responseText = handlers.getOrDefault(name, () -> "Unknown handler").get();
            byte[] response = responseText.getBytes(StandardCharsets.UTF_8);

            exchange.getResponseHeaders().add("Content-Type", "text/plain; charset=UTF-8");
            exchange.sendResponseHeaders(200, response.length);

            try (OutputStream os = exchange.getResponseBody()) {
                os.write(response);
            }
        });

        server.start();
        System.out.println("Server running on http://localhost:8080/run");
    }

    private static String getParam(String query, String key) {
        if (query == null) return "";
        for (String part : query.split("&")) {
            String[] kv = part.split("=", 2);
            if (kv.length == 2 && kv[0].equals(key)) {
                return kv[1];
            }
        }
        return "";
    }
}
