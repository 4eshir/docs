using System;
using System.Collections.Generic;
using System.Linq;

namespace infSearch6
{
    class Program
    {
        static void Main(string[] args)
        {
            string term = "april borrows three days of march, and they are ill.";
            string term1 = "who hath aching teeth, hath ill tenants.";
            string term2 = "he who hath no ill fortune, is dazed with good.";
            term = CleanString(term);
            term1 = CleanString(term1);
            term2 = CleanString(term2);
            double a = 1;
            double b = 0.4;
            double y = 0.35;
            Task1(term, term1, term2, a, b, y);

            Console.WriteLine();
            Console.WriteLine();

            term = "old men are twice children.";
            List<string> request = new List<string>() { "children are poor men's riches.", "critics are like brushers of other men's clothes.",
                                                        "rich men's spots are covered with money."};
            List<int> relev = new List<int>() { 0, 1 };
            Task2(term, request, relev);
        }

        public static void Task1(string term, string term1, string term2, double a, double b, double y)
        {
            List<List<int>> frequency = new List<List<int>>();
            List<string> words = term.Split(" ").ToList();
            words = words.Distinct().ToList();
            words.Sort();
            for (int i = 0; i != words.Count; ++i)
            {
                frequency.Add(new List<int>());
                frequency[frequency.Count - 1].Add(CountFrequency(term, words[i]));
                frequency[frequency.Count - 1].Add(CountFrequency(term1, words[i]));
                frequency[frequency.Count - 1].Add(CountFrequency(term2, words[i]));
            }

            List<double> rokkyVector = new List<double>();
            for (int i = 0; i != frequency.Count; ++i)
                rokkyVector.Add(a * (frequency[i][0] * 1.0) + b * (frequency[i][1] * 1.0) - y * (frequency[i][2] * 1.0));

            for (int i = 0; i != rokkyVector.Count; ++i)
                if (rokkyVector[i] < 0)
                    rokkyVector[i] = 0;

            Console.Write("Вектор Роккио: (");
            for (int i = 0; i != rokkyVector.Count - 1; ++i)
                Console.Write(rokkyVector[i] + " ");
            Console.Write(rokkyVector[rokkyVector.Count - 1] + ")");


        }

        public static int CountFrequency(string term, string word)
        {
            List<string> lstTerm = term.Split(" ").ToList();
            int c = 0;
            for (int i = 0; i != lstTerm.Count; ++i)
                if (lstTerm[i] == word)
                    c++;
            return c;
        }

        public static string CleanString(string s)
        {
            string newS = "";
            for (int i = 0; i != s.Length; ++i)
                if (s[i] != '.' && s[i] != ',' && s[i] != '!' && s[i] != '?')
                    newS += s[i];
            return newS;
        }

        public static void Task2(string term, List<string> request, List<int> relev)
        {
            List<string> words = term.Split(" ").ToList();
            for (int i = 0; i != request.Count; ++i)
            {
                List<string> temp = request[i].Split(" ").ToList();
                for (int j = 0; j != temp.Count; ++j)
                    words.Add(temp[i]);
            }
            words = words.Distinct().ToList();
            words.Sort();

            List<List<int>> frequency = new List<List<int>>();
            for (int i = 0; i != words.Count; ++i)
            {
                frequency.Add(new List<int>());
                for (int j = 0; j != relev.Count; ++j)
                    frequency[i].Add(CountFrequency(request[relev[j]], words[i]));

            }

            List<double> rokkyVector = new List<double>();
            for (int i = 0; i != frequency.Count; ++i)
            {
                double avg = 0;
                for (int j = 0; j != frequency[i].Count; ++j)
                    avg += frequency[i][j];
                avg = avg / (frequency[i].Count * 1.0);
                if (avg < 0) avg = 0;
                rokkyVector.Add(avg);
            }

            Console.Write("Вектор Роккио: (");
            for (int i = 0; i != rokkyVector.Count - 1; ++i)
                Console.Write(rokkyVector[i] + " ");
            Console.Write(rokkyVector[rokkyVector.Count - 1] + ")");
        }

    }
}
