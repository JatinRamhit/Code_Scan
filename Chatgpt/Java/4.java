import com.sun.net.httpserver.HttpExchange;
import com.sun.net.httpserver.HttpServer;
import java.io.OutputStream;
import java.net.InetSocketAddress;
import java.nio.charset.StandardCharsets;
import java.time.LocalDateTime;

public class SafeSystemInfoApi {
    public static void main(String[] args) throws Exception {
        HttpServer server = HttpServer.create(new InetSocketAddress(8080), 0);

        server.createContext("/info", exchange -> {
            String query = exchange.getRequestURI().getQuery();
            String action = getParam(query, "action");
            String result;

            if ("time".equals(action)) {
                result = LocalDateTime.now().toString();
            } else if ("java_version".equals(action)) {
                result = System.getProperty("java.version");
            } else if ("os".equals(action)) {
                result = System.getProperty("os.name");
            } else {
                result = "Allowed actions: time, java_version, os";
            }

            byte[] response = result.getBytes(StandardCharsets.UTF_8);
            exchange.getResponseHeaders().add("Content-Type", "text/plain; charset=UTF-8");
            exchange.sendResponseHeaders(200, response.length);

            try (OutputStream os = exchange.getResponseBody()) {
                os.write(response);
            }
        });

        server.start();
        System.out.println("Server running on http://localhost:8080/info");
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
